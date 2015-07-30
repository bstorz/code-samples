package jtalk;
import java.io.*;
import java.net.*;
import java.util.*;

// Basic class that sets up each client
class Client{
    String name;
    Socket socket;
    PrintWriter writer;

    Client(String name, Socket socket, PrintWriter writer){
        this.name = name;
        this.socket = socket;
        this.writer = writer;
    }
}


//Class to store a chatroom, its members, and notify those members of things
class ChatData {
    private String name;
    private ArrayList<Client> clients = new ArrayList<Client>();
    public ChatData(String name) {
		this.name = name;
	}
    public String getName(){
        return this.name;
    }
    public ArrayList<Client> getClients(){
        return this.clients;
    }

    //Join the chatroom
    public synchronized void join(String name, Socket socket){
        try{
            PrintWriter writer = new PrintWriter(socket.getOutputStream(),true);
            Client client = new Client(name,socket,new PrintWriter(socket.getOutputStream(),true));
            this.clients.add(client);
            announce(client.name+" joined");
        }
        catch(IOException e){
            e.printStackTrace();
        }
    }

    //Leave the chatroom
    public synchronized void leave(Socket socket){
        for(Client client : this.clients){
            if(client.socket == socket){
                announce(client.name+" left");
                this.clients.remove(client);
                return;
            }
        }
    }

    //Announce a message to everyone in the chat room
    public synchronized void announce(String message){
        for(Client client : this.clients) client.writer.println(message);
    }

    //List all peers in the chatroom
    public synchronized void listPeers(PrintWriter writer){
        for(Client client : this.clients) writer.println(client.name);
    }
}

