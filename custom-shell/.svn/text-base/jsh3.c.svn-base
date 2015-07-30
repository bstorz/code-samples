#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <fcntl.h>
#include "fields.h"
#include "dllist.h"
#include "jrb.h"
#include "jsh3.h"

//Prints the Prompt
//Screw the colon crap.  ">" is for winners.
void prompt(){
	printf("jsh3: ");
}

//Basic Error Message
void error(char *msg,int shouldExit){
	fprintf(stderr,"%s",msg);
	if(shouldExit) exit(1);
}

//Duplicates a String Array
char **strArrDup(char **strArr,int start,int size){
	int i;
	char ** newStrArr = calloc(size+1,sizeof(char *)); //Extra 1 is created for null termination
	for(i=start;i<start+size;i++) newStrArr[i-start] = strArr[i];
	return newStrArr;
}

//Handles Files and returns File Descriptor.
//Accepts 0 for read, 1 for write, and 2 for append
int prepareFD(int rwa,char * filename){
	int fd;
	if(rwa == 0) fd = open(filename, O_RDONLY); //read
	else if(rwa == 1) fd = open(filename, O_WRONLY | O_TRUNC | O_CREAT, 0644); //write
	else if(rwa == 2) fd = open(filename, O_WRONLY | O_APPEND | O_CREAT, 0644); //append
	else return -1;

	if (fd < 0){
		perror("jsh3: invalid filename");
		return -1;
	}
	return fd;
}

//Redirect File Descriptors
int redirectFDtoFD(int fd,int fd2){
	if(dup2(fd,fd2) != fd2){
		perror("jsh3: dup2(fd,fd2)");
		return;
	}
	close(fd);
}

//Processes String Array into commands and executes them
void handle_commands(char **fields,int NF){
	int i=0,j,cmdStart = 0,pipefd[2],opipefd[2];
	Dllist commands = new_dllist(),tmp;
	cmd *thecmd=NULL,*oldcmd=NULL;

	for(i=0;i<=NF;i++){
		if((i==NF || strcmp(fields[i],"|") == 0) && i != cmdStart){
			//Save the old cmd
			oldcmd = thecmd;
			if(oldcmd != NULL) oldcmd->pipeout = 1;

			//Create a cmd
			thecmd = malloc(sizeof(cmd));
			thecmd->size = i-cmdStart;
			thecmd->cmd = strArrDup(fields,cmdStart,i);
			thecmd->willWait = 1;
			thecmd->pipein = -1;
			thecmd->pipeout = -1;
			if(oldcmd != NULL) thecmd->pipein = 1;

			//Save it and prepare for the next one
			dll_append(commands,new_jval_v(thecmd));
			if(i+1<NF) cmdStart = i+1;
		}
	}

	//Prepare input to be a command
	int first = 1;
	dll_traverse(tmp,commands){
		thecmd = jval_v(tmp->val);
		
		//Parse Individual Commands
		parse_command(thecmd);

		//Use the pipefd[0] from the last go around if needed
		if(thecmd->pipein == 1) thecmd->pipein = pipefd[0];
		else if(first == 1) first = 0;
		else close(pipefd[0]);

		//Setup pipe for the next one.
		if(thecmd->pipeout == 1){
			j = pipe(pipefd);
			if(j < 0) perror("jsh3: pipe");
			else thecmd->pipeout = pipefd[1];
		}

		//Do the command
		do_command(thecmd);
	}
}

//Handles Individual Commands
void parse_command(cmd *thecmd){
	int i = 0,cmdEnd = -1, redirectIn = 0, redirectOut = 0, redirectAppend = 0;
	char *rIn=NULL,*rOut=NULL,*rApp=NULL;
	char **command = NULL;

	command = thecmd->cmd;

	//Handles Redirection
	for(i=0;i<thecmd->size;i++){
		if(strcmp(command[i],"<") == 0 && redirectIn == 0){
			redirectIn = 1;

			if(i+1<thecmd->size) rIn = command[i+1];
			cmdEnd = i;
		}
		else if(strcmp(command[i],">") == 0 && redirectOut == 0){
			redirectOut = 1;
			redirectAppend = 0;

			rApp = NULL;
			if(i+1<thecmd->size) rOut = command[i+1];
			cmdEnd = i;
		}
		else if(strcmp(command[i],">>") == 0 && redirectOut == 0 && redirectAppend == 0){
			redirectAppend = 1;
			if(i+1<thecmd->size) rApp = command[i+1];
			cmdEnd = i;
		}
	}
	if(cmdEnd == -1) cmdEnd = i;

	//Redirect Input and Output to files if needed
	if(thecmd->pipein == -1 && redirectIn == 1) thecmd->pipein = prepareFD(0,rIn);
	if(thecmd->pipeout == -1 && (redirectOut == 1 || redirectAppend == 1)) thecmd->pipeout = prepareFD(redirectAppend+1,(rOut == NULL)?rApp:rOut);

	//Handles &
	if(strcmp(command[cmdEnd-1],"&") == 0) thecmd->willWait = 0;

	//Handle Wait & Save our modifications to command
	if(strcmp(command[cmdEnd-1],"&") == 0){
		thecmd->willWait = 0;
		thecmd->cmd = strArrDup(command,0,cmdEnd-1);
		
		//Have to wait to be able to redirect
		if(thecmd->pipeout != -1){
			thecmd->willWait = 1;
			error("jsh3: invalid syntax. ignoring '&'\n",0);
		}
	}
	else thecmd->cmd = strArrDup(command,0,cmdEnd);
	free(command);
}

//Fork and execute commands
int do_command(cmd *command){
	int status,pid,i;
	pid = fork();
	if(pid == 0){
		if(command->pipein != -1) redirectFDtoFD(command->pipein,0);
		if(command->pipeout != -1) redirectFDtoFD(command->pipeout,1);

		//Execute Command
		execvp(command->cmd[0], command->cmd);
		perror("jsh3");
		exit(1);
	}
	else if(command->willWait == 1){
		//Wait for whatever until we finally get to the new child.
		for(i=wait(&status);i!=-1&&i!=pid;i=wait(&status)){}
	}

	//Close Our Pipes
	if(command->pipein != -1) close(command->pipein);
	if(command->pipeout != -1) close(command->pipeout);
	return status;
}

void quit_shell(){
	exit(0);
}

int main(int argc, char **argv){
	IS input;
	
	//Accept Arguments as a Command
	if(argc > 1){
		handle_commands(strArrDup(argv,1,argc-1),argc-1);
		quit_shell();
	}
	
	//Accept Std in as a Command
	input = new_inputstruct(NULL);
	if(input == NULL){
		perror("jsh3");
		error("invalid input\n",1);
	}

	prompt();
	while(get_line(input) >=0){
		if(strcmp(input->text1,"\n")==0){
			prompt();
			continue;
		}
		if(strcmp(input->fields[0],"exit") == 0) quit_shell();
		handle_commands(input->fields,input->NF);
		prompt();
	}

	return 0;
}
