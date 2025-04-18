local DEBUG = true

local handlers = nil
local server = nil

if DEBUG then
    server = include('resources/server.lua')
    handlers = include('resources/handlers.lua')
else
    handlers = require('resources/handlers')
    server = require('resources/server')
end

local PORT = 12345
local HOST = "0.0.0.0"

local mod = RegisterMod("Danin -- Isaac", 1)

function mod:onUpdate()
	if not server.isRunning() then
		return
	end

    local client = server.getClient()
    local msg = server.receive()

    if msg then
        handlers.handle(msg)
    end
end

function mod:tearUp()
    server.start(HOST, PORT)
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
