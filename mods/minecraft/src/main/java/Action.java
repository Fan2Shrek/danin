package danin.main;

import net.minecraft.entity.player.PlayerEntity;
import net.minecraft.server.world.ServerWorld;

import java.util.concurrent.Callable;
import java.util.ArrayList;
import java.util.List;
import java.util.function.BiConsumer;

public class Action {
    private static Action instance;
    private List<BiConsumer<ServerWorld, PlayerEntity>> actions;

    private Action() {
        actions = new ArrayList<>();
    }

    public static void add(BiConsumer<ServerWorld, PlayerEntity> callback) {
        init();

        instance.actions.add(callback);
    }

    public static List<BiConsumer<ServerWorld, PlayerEntity>> getActions() {
        init();
        List<BiConsumer<ServerWorld, PlayerEntity>> actions = new ArrayList<>(instance.actions);
        instance.actions.clear();

        return actions;
    }

    private static void init() {
        if (instance == null) {
            instance = new Action();
        }
    }
}
