local DEBUG = true

local handlers = nil
local server = nil

local console = require("resources.console")

if DEBUG then
    server = include('resources/server.lua')
    handlers = include('resources/handlers.lua')
else
    handlers = require('resources/handlers')
    server = require('resources/server')
end

local PORT = 12345
local HOST = "localhost"

local mod = RegisterMod("Danin -- Isaac", 1)

local request=[=[
GET /.well-known/mercure?topic=aaa HTTP/1.1
Host: localhost:12345
Accept: text/event-stream
Connection: keep-alive


]=]


local socket = require("socket")





local client = assert(socket.tcp())
client:settimeout(0)
client:connect("127.0.0.1", 8090)


function mod:onUpdate()
	-- if not server.isRunning() then
	-- 	return
	-- end
	--
	--    local client = server.getClient()
	--    local msg = server.receive()
	--
	--    if msg then
	--        handlers.handle(msg)
	--    end

    local line, err = client:receive("*l")
    if line then
        -- Simple SSE parsing
        if line:match("^data:") then
            local data = line:sub(6)
	        handlers.handle(data)
        end
    else
        if err ~= "timeout" then
            console.debug("Connection closed or error:".. err)
        end
    end
end

function mod:tearUp()
    -- server.start(HOST, PORT)

    client:send(request)

    local response, err = client:receive()
    if response then
        console.debug("Received from server:".. response)
    else
        console.debug("Receive error:".. err)
    end
end

function mod:tearDown()
    server.stop()
    handlers.tearDown()
end

-- Make sure the mod is started if using luamod from cli
if Game():GetFrameCount() > 0 then
    mod:tearUp()
end

mod:AddCallback(ModCallbacks.MC_POST_UPDATE, mod.onUpdate)

mod:AddCallback(ModCallbacks.MC_POST_GAME_STARTED, mod.tearUp)

mod:AddCallback(ModCallbacks.MC_PRE_GAME_EXIT, mod.tearDown)
mod:AddCallback(ModCallbacks.MC_POST_GAME_END, mod.tearDown)
mod:AddCallback(ModCallbacks.MC_PRE_MOD_UNLOAD, mod.tearDown)
