local socket = require("socket")
local console = require("resources.console")

local mercure = {}

local url_pattern = "^(https?)://([^:/]+):?(%d*)(/.*)$"
local request=[=[
GET %s HTTP/1.1
Accept: text/event-stream
Authorization: Bearer %s
Connection: keep-alive
Host: %s:%d

]=]

local client = nil;
local token = nil;

function mercure.start(url, jwt)
    local protocol, hostname, port, path = url:match(url_pattern)
    if not hostname then
        error("Invalid host format: " .. tostring(url))
    end
    port = tonumber(port) or 80

    if not client then
        client = assert(socket.tcp())
        client:settimeout(0)
        client:connect(hostname, port)
    end

    token = jwt

    client:send(string.format(request, path, token, hostname, port))
end

function mercure.stop()
    if client then
        client:close()
        client = nil
    end

    console.debug("Mercure stopped")
end

function mercure.process()
    local line, err = client:receive("*l")

    if line then
        if line:match("^data:") then
            local data = line:sub(6)

            return data
        end
    else
        if err ~= "timeout" then
            console.debug("Connection closed or error:".. err)
        end
    end
end

return mercure;
