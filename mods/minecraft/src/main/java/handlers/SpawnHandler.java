package danin.handlers;

import danin.handlers.Handler;
import danin.main.Action;

import net.minecraft.entity.EntityType;
import net.minecraft.entity.player.PlayerEntity;
import net.minecraft.entity.mob.ZombieEntity;
import net.minecraft.server.world.ServerWorld;

public class SpawnHandler implements Handler {
    public Boolean supports(String type) {
        return type.equals("spawn");
    }

    public void handle(String content) {
        System.out.println("Spawn: " + content);

        Action.add((ServerWorld world, PlayerEntity player) -> {
            ZombieEntity zombie = new ZombieEntity(EntityType.ZOMBIE, world);
            zombie.refreshPositionAndAngles(player.getX(), player.getY(), player.getX(), 0.0F, 0.0F);
            world.spawnEntity(zombie);
        });
    }
}
