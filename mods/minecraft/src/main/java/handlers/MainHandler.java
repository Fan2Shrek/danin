package danin.handlers;

import danin.handlers.Handler;

import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

public class MainHandler implements Handler {
    private Handler[] handlers;

    public MainHandler() {
        this.handlers = new Handler[] {
            new SpawnHandler(),
        };
    }

    public Boolean supports(String type) {
        for (Handler handler : this.handlers) {
            if (handler.supports(type)) {
                return true;
            }
        }
        return false;
    }

    public void handle(String content) {
        JsonObject json = JsonParser.parseString(content).getAsJsonObject();

        String type = json.get("type").getAsString();
        String data = json.get("content").getAsString();

        for (Handler handler : this.handlers) {
            if (handler.supports(type)) {
                handler.handle(data);

                return;
            }
        }
    }
}
