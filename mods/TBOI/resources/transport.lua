local console = require("resources.console");
local server = require("resources.server");
local json = require("json");

local web = include("resources/web.lua");
local mercure = include("resources/mercure.lua");

local transport = {};

local isRunning = false;
local mercure_initialized = false;

local function startMercure(msg)
    if mercure_initialized then
        console.debug("Mercure transport already initialized.");
        return;
    end

    console.debug("Starting Mercure transport...");
    local success, data = pcall(json.decode, msg)

    if not success then
        console.debug("Failed to decode JSON: " .. tostring(msg));
        return;
    end

    mercure.start(data.mercureUrl, data.mercureToken);
    console.debug("Mercure transport started successfully.");
    mercure_initialized = true;
    -- web.stop();
end

function transport.setUp()
    console.debug(string.format("Setting up transport (%s)...", TRANSPORT_TYPE));

    if TRANSPORT_TYPE == "mercure" then
        web.start(HOST, WEB_PORT);
    elseif TRANSPORT_TYPE == "socket" then
        server.start(HOST, MOD_PORT);
    end

    isRunning = true;

    console.debug("Transport set up successfully.");
end

function transport.stop()
    if TRANSPORT_TYPE == "mercure" then
        web.stop();
    elseif TRANSPORT_TYPE == "socket" then
        server.stop();
    end

    isRunning = false;
end

function transport.process()
    if not isRunning then
        console.debug("Transport is not running, skipping processing.");
        return;
    end

    local msg = nil;

    if TRANSPORT_TYPE == "mercure" then
        if not mercure_initialized then
            msg = web.receive();

            if msg then
                startMercure(msg);
            end

            return;
        else
            msg = mercure.process();
        end
    elseif TRANSPORT_TYPE == "socket" then
        msg = server.stop();
    end

    return msg;
end

return transport;
