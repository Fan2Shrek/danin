package danin.test;

import net.fabricmc.api.ModInitializer;
import net.fabricmc.fabric.api.event.lifecycle.v1.ServerTickEvents;
import net.minecraft.server.world.ServerWorld;
import net.minecraft.entity.player.PlayerEntity;

import java.io.IOException;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import danin.main.Action;
import danin.main.Server;

import java.util.function.BiConsumer;

public class Danin implements ModInitializer {
    public static int PORT = 12345;
	public static final String MOD_ID = "danin";
	public static final Logger LOGGER = LoggerFactory.getLogger(MOD_ID);

	@Override
	public void onInitialize() {
        this.tearUp();
		LOGGER.info("Hello Fabric world!");
	}

    public void tearUp() {
        Server server = new Server(this.PORT);
        server.start();

        ServerTickEvents.END_SERVER_TICK.register(tick -> {
            for (BiConsumer<ServerWorld, PlayerEntity> callback : Action.getActions()) {
                for (ServerWorld world : tick.getWorlds()) {
                    for (PlayerEntity player : world.getPlayers()) {
                        callback.accept(world, player);
                    }
                }
            }
        });
    }
}
