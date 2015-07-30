package jtalk;
import java.io.*;
import java.net.*;

public class TalkClient{
    void printRooms(BufferedReader reader){
        System.out.printf("Chat Rooms:%n%n");
        try{
            String line = reader.readLine();
            while(line != null && !line.equals("endRooms")){
                System.out.printf("%s:",line);
                
                line = reader.readLine();
                while(line != null && !line.equals("endParticipants")){
                    System.out.printf(" %s", line);    
                    line = reader.readLine();
                }

                System.out.println();
                line = reader.readLine();
            }
            System.out.println();
        }
        catch(IOException e){
            e.printStackTrace();
            System.exit(1);
        }
    }
    
    public static void main(String[] args) throws IOException {
        if(args.length != 3){
            System.err.printf("Usage: java TalkClient <host name> <port number> <user name>%n");
            System.exit(1);
        }
        new TalkClient(args[0],Integer.parseInt(args[1]),args[2]);
    }

    public TalkClient(String hostname, int port, String user) throws IOException {
        Socket socket = new Socket(hostname, port);
        PrintWriter serverInput = new PrintWriter(socket.getOutputStream(),true);
        BufferedReader serverOutput = new BufferedReader(new InputStreamReader(socket.getInputStream()));
        BufferedReader input = new BufferedReader(new InputStreamReader(System.in));
        try{
            serverInput.printf("%s%n",user);
            printRooms(serverOutput);
            
            String response = "false";
            while(response.equals("false")){
                System.out.printf("Enter Chat Room:%n");
                serverInput.printf("%s%n",input.readLine());
                response = serverOutput.readLine();
            }

            TalkClientThread write = new TalkClientThread(socket, hostname, serverOutput);
            write.start();
            for(String cmd = input.readLine(); cmd != null; cmd = input.readLine()){
                serverInput.printf("%s%n",cmd);
            }
        }
        catch(UnknownHostException e){
            System.err.printf("Host %s does not exist.%n", hostname);
            System.exit(1);
        }
        catch(IOException e){
            System.err.printf("No I/O for connection to host %s%n",hostname);
        }
        finally{
            if(socket != null) socket.close();
            if(serverInput != null) serverInput.close();
            if(serverOutput != null) serverOutput.close();
            if(input != null) input.close();
        }
    }
}
