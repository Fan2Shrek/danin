local socket = require("socket")
local json = require("json")

local PORT = 12345
local HOST = "0.0.0.0"
local DEBUG = true

local function iprint(str)
    Isaac.ConsoleOutput(str)
end

local function debug(str)
    if str == nil then
        str = "(nil)"
    end

    if DEBUG then
        iprint("<debug>" .. str .. "<debug>\n\n")
    end
end

local mod = RegisterMod("Danin -- Isaac", 1)
local server = assert(socket.bind(HOST, PORT))
server:settimeout(0)
local client = nil

iprint("Server started on " .. HOST .. ":" .. PORT .. "\n")

local function useActiveItem(id)
    local player = Isaac.GetPlayer()

    if id == nil then
        id = player:GetActiveItem()
    end

    player:UseActiveItem(id)
end

local function spawn(entity)
    if not type(entity) == "number" then
        debug(entity)

        return
    end

	Isaac.Spawn(entity, PickupVariant.PICKUP_COLLECTIBLE, 0, Vector(320, 280), Vector(0, 0), nil)
end

local function handle(data)
    local type = data.type
    local content = data.content

    if type == "spawn" then
        spawn(content)
    elseif type == "activate" then
        useActiveItem(content)
    end
end

function mod:onUpdate()
    if not client then
        client = server:accept()

        if client then
            client:settimeout(0)
            iprint("Client connected")
        end
    end
    if client then
        local msg, err = client:receive()

        if msg then
            handle(json.decode(msg))
        elseif err ~= "timeout" then
            debug("Error on connection:" .. err)
            client:close()
            client = nil
        end
    end
end

mod:AddCallback(ModCallbacks.MC_POST_UPDATE, mod.onUpdate)
