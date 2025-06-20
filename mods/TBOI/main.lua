DEBUG = true
TRANSPORT_TYPE = "mercure";
-- TRANSPORT_TYPE = "socket";

local handlers = nil
local transport = nil;

if DEBUG then
    handlers = include('resources/handlers.lua')
    transport = include('resources/transport.lua')
else
    handlers = require('resources/handlers')
    transport = require('resources.transport')
end

HOST = "0.0.0.0"
MOD_PORT = 12345
WEB_PORT = 11664;

local mod = RegisterMod("Danin -- Isaac", 1)

function mod:onUpdate()
    local msg = transport.process();

    if msg then
       handlers.handle(msg)
    end
end

function mod:tearUp()
    transport.setUp();
end

function mod:tearDown()
    transport.stop();
    handlers.tearDown();

    SFXManager():StopLoopingSounds()
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
