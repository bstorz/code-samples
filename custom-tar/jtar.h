/* Brandon Storz
   CS360
   12/10/13
*/

#ifndef jtar
#define jtar
#ifdef _cplusplus
extern "C" {
#endif

void error(char *message,int should_exit);
void make_tar(Dllist filenames);
Dllist get_filenames_from_args(int argc, char **argv);
void write_info_to_tar(char *name,struct stat fileStat);
void write_contents_to_tar(char *name,int size);
void process_dir(char *dirpath,JRB inodes);
void extract_tar();
char *get_path(int n, char **fields);
void write_contents(char *filename,int size);

#ifdef _cplusplus
}
#endif

#endif
