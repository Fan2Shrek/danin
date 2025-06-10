local socket = require("socket")
local console = require("resources.console")

local mercure = {}

local url_pattern = "^(https?)://([^:/]+):?(%d*)(/.*)$"
local request=[=[
GET %s HTTP/1.1
Accept: text/event-stream
Cookie: mercureAuthorization=%s
Connection: keep-alive
Host: %s:%d
User-Agent: IsaacMod/1.0

]=]

local client = nil;
local token = nil;

function mercure.start(url, jwt)
    local protocol, hostname, port, path = url:match(url_pattern)
    if not hostname then
        error("Invalid host format: " .. tostring(url))
    end
    port = tonumber(port) or (protocol == "https" and 443 or 80)

    if not client then
        client = assert(socket.tcp())
        client:settimeout(0)
        client:connect(hostname, port)
    end

    token = jwt

    console.debug(string.format(request, path, token, hostname, port))
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
    if not client then
        return nil
    end

    local line, err = client:receive("*l")

    if line then
        console.debug("Received line: " .. line)
        if line:match("^data:") then
            local data = line:sub(6)

            return data
        end
    else
        if err ~= "timeout" then
            console.debug("Connection closed or error:".. err)
            mercure.stop();
        end
    end
end

return mercure;
