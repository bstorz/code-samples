#ifndef _JSH_H
#define _JSH_H

#ifdef __cplusplus
extern "C" {
#endif

struct Dllist;
typedef struct cmd{
	char **cmd;
	int size;
	int willWait;
	int pipein;
	int pipeout;
} cmd;

void prompt();
void error(char *msg,int shouldExit);
void handle_commands(char **fields,int NF);
void parse_command(struct cmd *thecmd);
int do_command(struct cmd *command);
void quit_shell();

#ifdef __cplusplus
}  /* extern C */
#endif

#endif
