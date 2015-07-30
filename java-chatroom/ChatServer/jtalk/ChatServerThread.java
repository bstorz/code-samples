package jtalk;

import java.io.*;
import java.net.*;
import java.util.*;

public class ChatServerThread extends Thread {
    private Socket socket = null;
    private ChatData chatroom;
    private TreeMap<String, ChatData> chatrooms;

	public ChatServerThread(Socket socket, TreeMap<String,ChatData> chatrooms) {
		this.socket = socket;
        this.chatrooms = chatrooms;
	}

    //Offer the client a prompt to select the chatroom
    ChatData selectChatroom(BufferedReader clientOutput, PrintWriter clientInput) throws IOException{
        ChatData chat = null;
        //Print the rooms and peoples in them
        for(Map.Entry entry : this.chatrooms.entrySet()){
            chat = (ChatData)entry.getValue();
            clientInput.println(chat.getName());
            chat.listPeers(clientInput);
            clientInput.println("endParticipants");
        }
        clientInput.println("endRooms");

        //Wait for user response and find the room inputted
        chat = null;
        while(chat == null){
            chat = this.chatrooms.get(clientOutput.readLine());
            if(chat == null) clientInput.println("false");
            else{
                clientInput.println("true");
                return chat;
            }
        }
        return chat;
    }

    public void run(){
        BufferedReader clientOutput = null;
        PrintWriter clientInput = null;
        String name = "";
        String message = "";

        try{
            clientOutput = new BufferedReader(new InputStreamReader(this.socket.getInputStream()));
            clientInput = new PrintWriter(this.socket.getOutputStream(), true);
            name = clientOutput.readLine();

            //Setup the Chatroom for the client to use
            this.chatroom = selectChatroom(clientOutput,clientInput);
            this.chatroom.join(name,this.socket);

            //Send the messages
            for(message = clientOutput.readLine();message != null;message=clientOutput.readLine()){
                this.chatroom.announce(name + ": " + message);
            }

            //Leave when we finish
            this.chatroom.leave(this.socket);
        }
        catch(IOException e){
            e.printStackTrace();
        }
        
        // Close everything
        try{
            this.socket.close();
            if(clientOutput != null) clientOutput.close();
            if(clientInput != null) clientInput.close();
        }
        catch(IOException e){
            System.out.println(name + ": I/O error");
            e.printStackTrace();
        }
    }
}
