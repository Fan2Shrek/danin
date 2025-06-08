local console = {}

function console.print(str)
    Isaac.ConsoleOutput(str)
end

function console.debug(str)
    if str == nil then
        str = "(nil)"
    end
    if DEBUG then
        console.print("[DEBUG] " .. str .. "\n")
    end
end

return console
