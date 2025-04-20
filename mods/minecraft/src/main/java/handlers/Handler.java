package danin.handlers;

public interface Handler {
    void handle(String content);
    Boolean supports(String type);
}
