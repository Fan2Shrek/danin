local socket = require("socket");
local console = require("resources.console");

local web = {}

local connection = nil;
local client = nil;

local response = table.concat({
    "HTTP/1.1 200 OK",
    "Content-Type: application/json",
    "Access-Control-Allow-Origin: *",
    "Access-Control-Allow-Methods: POST, OPTIONS",
    "Access-Control-Allow-Headers: Content-Type",
    "Content-Length: %d",
    "",
    "%s"
}, "\r\n")

function web.start(host, port)
    connection = assert(socket.bind(host, port))
    connection:settimeout(0)
    console.debug("Web server started on " .. host .. ":" .. port)
end

function web.stop()
    if client then
        client:close()
        client = nil
    end

    if connection then
        connection:close()
        connection = nil
    end
    console.debug("Web server stopped")
end

function web.receive()
    if not connection then
        console.debug("Web server is not running")
        return nil
    end

    client = connection:accept()

    if client then
        client:settimeout(0.5)

        local request_line = client:receive()
        if not request_line then
            client:close()
            return nil
        end

        local method = request_line:match("^(%w+)")
        console.debug("Request method: " .. tostring(method))

        -- headers
        local headers = {}
        local line
        local content_length = 0
        repeat
            line = client:receive()
            if line and line ~= "" then
                table.insert(headers, line)
                local key, value = line:match("^(.-):%s*(.*)$")
                if key and key:lower() == "content-length" then
                    content_length = tonumber(value)
                end
            end
        until not line or line == ""

        if method == "OPTIONS" then
            local response = "HTTP/1.1 204 No Content\r\n" ..
                             "Access-Control-Allow-Origin: *\r\n" ..
                             "Access-Control-Allow-Methods: POST, OPTIONS\r\n" ..
                             "Access-Control-Allow-Headers: Content-Type\r\n" ..
                             "Content-Length: 0\r\n\r\n"
            client:send(response)
            client:close()
            return nil
        end

        -- body
        local body = nil
        if content_length and content_length > 0 then
            body = client:receive(content_length)
        end

        if body then
            console.debug("Body:\n" .. body)
        end

        local response_body = "{\"status\":\"success\"}"
        client:send(string.format(response, #response_body, response_body))
        client:close()

        if body then
            return body;
        end;
    end
end

return web
