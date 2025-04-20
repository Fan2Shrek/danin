package danin.test;

import net.fabricmc.fabric.api.networking.v1.ServerPlayNetworking;
import net.minecraft.entity.Entity;
import net.minecraft.entity.EntityType;
import net.minecraft.network.PacketByteBuf;
import net.minecraft.registry.Registries;
import net.minecraft.server.MinecraftServer;
import net.minecraft.server.network.ServerPlayerEntity;
import net.minecraft.server.world.ServerWorld;
import net.minecraft.util.Identifier;
import net.minecraft.util.math.BlockPos;

public class ServerHandler {
    public static final Identifier SPAWN_MOB_PACKET_ID = Identifier.of("mymod", "spawn_mob");

    public static void register() {
        ServerPlayNetworking.registerGlobalReceiver(SPAWN_MOB_PACKET_ID, (payload, context) -> {
            String mobName = payload.readString();
            double x = payload.readDouble();
            double y = payload.readDouble();
            double z = payload.readDouble();

            context.player().getServer().execute(() -> {
                spawnMob(context.player().getServer(), context.player(), mobName, x, y, z);
            });
        });
    }

    private static void spawnMob(MinecraftServer server, ServerPlayerEntity player, String mobName, double x, double y, double z) {
        ServerWorld world = (ServerWorld) player.getWorld();
        BlockPos pos = new BlockPos(x, y, z);

        Identifier mobId = Identifier.of("minecraft", mobName);
        EntityType<?> type = Registries.ENTITY_TYPE.get(mobId);
        if (type == null) {
            System.out.println("Unknown mob type: " + mobName);
            return;
        }

        Entity entity = type.create(world);
        if (entity != null) {
            entity.refreshPositionAndAngles(pos, 0.0F, 0.0F);
            world.spawnEntity(entity);
            System.out.println("Spawned: " + mobName + " at " + pos);
        } else {
            System.out.println("Failed to create entity for: " + mobName);
        }
    }
}
