package jtalk;
import java.io.*;
import java.net.*;
class TalkClientThread extends Thread{
    Socket socket;
    String hostname;
    BufferedReader serverOutput;
    TalkClientThread(Socket socket, String hostname, BufferedReader serverOutput){
        this.socket = socket;
        this.hostname = hostname;
        this.serverOutput = serverOutput;
    }   
    public void run(){
        try{
            for(String response = this.serverOutput.readLine();response != null;response = this.serverOutput.readLine()){
                System.out.printf("%s%n",response);
            }   
        }   
        catch(IOException e){ 
            System.err.printf("No I/O for connection to %s%n",hostname);
        }   
    }   
}
