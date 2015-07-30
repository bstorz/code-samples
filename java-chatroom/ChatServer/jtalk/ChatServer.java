package jtalk;
import java.net.*;
import java.io.*;
import java.util.*;

public class ChatServer {
	public ChatServer(int port, TreeMap<String, ChatData> chatrooms) throws IOException {
        //Setup the Socket and start creating threads as people join into it
        ServerSocket socket = new ServerSocket(port);
        try{
            while(true) new ChatServerThread(socket.accept(), chatrooms).start();
        }
        catch(IOException e){
            System.err.printf("Could not listen on port %d%n",port);
            System.exit(-1);
        }
	}
    public static void main(String[] args) throws IOException{
        // Check for valid input
        if(args.length < 2){
            System.err.printf("Usage: java ChatServer <port number> chatroom1 ... chatroom_n%n");
            System.exit(1);
        }

        int port = 0;
        try{
            port = Integer.parseInt(args[0]);
        }
        catch(NumberFormatException e){
            System.out.printf("Port number must be an integer");
            System.exit(1);
        }

        //Create a map and create each chatroom in it
        TreeMap<String,ChatData> chatrooms = new TreeMap<String,ChatData>();
        for(int k=1;k<args.length;k++) chatrooms.put(args[k], new ChatData(args[k]));

        //Startup the ChatServer
        new ChatServer(port, chatrooms);
    }
}

