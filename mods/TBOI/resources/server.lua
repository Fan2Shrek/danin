local socket = require("socket")
local console = require("resources.console")

local server = {}

local connection = nil
local client = nil

function server.start(host, port)
    connection = assert(socket.bind(host, port))
    connection:settimeout(0)
    console.print("Server started on " .. host .. ":" .. port .. "\n")
end

function server.stop()
    if client then
        client:close()
        client = nil
    end

    if connection then
        connection:close()
        connection = nil
    end

    console.print("Server closed\n")
end

function server.isRunning()
    return connection ~= nil
end

function server.getClient()
    if not server.isRunning() then
        return nil
    end

    if not client then
        client = connection:accept()
        if client then
            client:settimeout(0)
            console.print("Client connected\n")
        end
    end

    return client
end

function server.receive()
    if client then
        local msg, err = client:receive()
        if msg then
            return msg
        elseif err ~= "timeout" then
            console.debug("Socket error: " .. tostring(err))
            client:close()
            client = nil
        end
    end

    return nil
end

return server
