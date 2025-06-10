local socket = require("socket")
local console = require("resources.console")

local mercure = {}

local url_pattern = "^(https?)://([^:/]+):?(%d*)(/.*)$"
local requestTemplate=[=[
GET %s HTTP/1.1
Host: %s:%d
Accept: text/event-stream
Cookie: mercureAuthorization=%s
Connection: keep-alive
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
        client = socket.tcp()
        client:settimeout(0.2)
        client:connect(hostname, port)
    end

    token = jwt

    local request = string.format(requestTemplate, path, hostname, port, token)

    client:send(request)
    client:settimeout(0)
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
