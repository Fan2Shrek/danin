package danin.main;

import java.io.*;
import java.net.*;

import danin.handlers.*;

public class Server extends Thread {
    private ServerSocket serverSocket;
    private int port;
    private Boolean isRunning = false;
    private Handler handler;

    public Server(int port) {
        this.port = port;
        this.handler = new MainHandler();
    }

    public void connect() throws IOException {
        if (isRunning) {
            return;
        }

        serverSocket = new ServerSocket(port);
        isRunning = true;
    }

    @Override
    public void run() {
        try {
            connect();
        } catch (IOException e) {
            System.err.println("Failed to start server: " + e.getMessage());

            return;
        }

        while (isRunning) {
            try {
                Socket clientSocket = serverSocket.accept();
                System.out.println("Client connected: " + clientSocket.getInetAddress());

                BufferedReader in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));
                PrintWriter out = new PrintWriter(clientSocket.getOutputStream(), true);

                String inputLine;
                while ((inputLine = in.readLine()) != null) {
                    handler.handle(inputLine);
                }
            } catch (IOException e) {
                if (isRunning) {
                    e.printStackTrace();
                }
                break;
            }
        }

        stopServer();
    }

    public void stopServer() {
        isRunning = false;
        try {
            if (serverSocket != null && !serverSocket.isClosed()) {
                serverSocket.close();
                System.out.println("Server stopped.");
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
