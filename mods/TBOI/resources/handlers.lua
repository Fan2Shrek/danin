local ALLOW_TOKENLESS = true

local json = require("json")
local console = require("resources.console")

local handlers = {}

local token = nil

function handlers.spawn(entity)
	if type(entity) ~= "number" then
		console.debug(entity)

		return
	end

	Isaac.Spawn(entity, PickupVariant.PICKUP_COLLECTIBLE, 0, Vector(320, 280), Vector(0, 0), nil)
end

function handlers.useActiveItem(id)
	local player = Isaac.GetPlayer()

	if id == nil then
		id = player:GetActiveItem()
	end

	player:UseActiveItem(id)
end

function handlers.playSFX(id)
    -- SFXManager():Play(id, 10, 2, true, 1)
    SFXManager():Play(id) -- maybe pass multiple param
end

function handlers.handle(msg)
	local success, data = pcall(json.decode, msg)
    console.debug("Received message: " .. tostring(msg))

	if not success then
		console.debug("Failed to decode JSON: " .. tostring(msg))
		return
	end

    if not ALLOW_TOKENLESS then
        if nil == token and data.token and not data.content then
            token = data.token
            console.debug("Token set: " .. tostring(token))

            return
        end
    end

    if not ALLOW_TOKENLESS and data.token ~= token then
        console.debug("Invalid token: " .. tostring(data.token))
        return
    end

	local action = data.type
	local content = data.content

	if action == "spawn" then
		handlers.spawn(content)
	elseif action == "activate" then
		handlers.useActiveItem(content)
	elseif action == "sound" then
		handlers.playSFX(content)
	end
end

function handlers.tearDown()
    console.debug("Tearing down handlers")

    if token then
        token = nil
    end
end

return handlers
