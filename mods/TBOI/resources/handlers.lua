local json = require("json")

local console = require("resources.console")

local handlers = {}

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

function handlers.handle(msg)
	local success, data = pcall(json.decode, msg)

	if not success then
		console.debug("Failed to decode JSON: " .. tostring(msg))
		return
	end

	local action = data.type
	local content = data.content

	if action == "spawn" then
		handlers.spawn(content)
	elseif action == "activate" then
		handlers.useActiveItem(content)
	end
end

return handlers
