#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <dirent.h>
#include <utime.h>

#include "dllist.h"
#include "fields.h"
#include "jrb.h"
#include "jtar.h"

int verbose;

int main(int argc, char **argv){
	if(argc < 2) error("Incorrect number of arguments.\n",1);

	verbose = 0;
	int flagsize = strlen(argv[1]);

	if(flagsize > 2) error("Incorrect number of flags.\n",1);
	if(flagsize == 2){
		if(argv[1][1] == 'v') verbose = 1;
		else error("Incorrect flags.  Only c or x and v are accepted.",1);
	}

	if(argv[1][0] == 'c') make_tar(get_filenames_from_args(argc,argv));
	else if(argv[1][0] == 'x') extract_tar();
	else error("Incorrect flags.  Only c or x and v are accepted.\n",1);

	return;
}

void error(char *message,int should_exit){
	fprintf(stderr,"%s\n",message);
	if(should_exit) exit(1);	
}

/*
 *
 * CREATE TAR FILES
 *
 * */
Dllist get_filenames_from_args(int argc, char **argv){
    int i;
    Dllist list = new_dllist();
    for (i=2; i<argc; i++) dll_append (list, new_jval_s(argv[i]));
    return list;
}

void make_tar(Dllist filenames){
    int i;
    struct stat fileStat;
    Dllist tmp;
    JRB paths = make_jrb(), inodes = make_jrb();

    dll_traverse(tmp, filenames){
        if(lstat(tmp->val.s, &fileStat) < 0) perror(tmp->val.s);
        else if(jrb_find_int(paths,fileStat.st_ino) == NULL){
            jrb_insert_int(paths, fileStat.st_ino,JNULL);
            if(S_ISDIR(fileStat.st_mode)){
                write_info_to_tar(tmp->val.s,fileStat);
                if(verbose) fprintf(stderr,"Directory: ./%s    %i bytes\n",tmp->val.s,(int)fileStat.st_size);
                process_dir(tmp->val.s,inodes);
            }
            else if(S_ISREG(fileStat.st_mode)){
                write_info_to_tar(tmp->val.s,fileStat);
                if(verbose) fprintf(stderr,"File: ./%s    %i bytes\n",tmp->val.s,(int)fileStat.st_size);
                write_contents_to_tar(tmp->val.s,fileStat.st_size);
            }
        }
        else{
            write_info_to_tar(tmp->val.s,fileStat);
            if(verbose) fprintf(stderr,"Link: ./%s\n",tmp->val.s);
        }
    }
    jrb_free_tree(paths);
    jrb_free_tree(inodes);
}
void write_info_to_tar(char *name, struct stat fileStat){
	printf("%s\n",name);
	fwrite(&fileStat, sizeof(struct stat), 1, stdout);
}
void write_contents_to_tar(char *name,int size){
    void *buffer;
    int i=0,bytes_read;
    IS input = new_inputstruct(name);
    if(input == NULL){
        perror("jtar");
        exit(1);
    }

	buffer = malloc(MAXLEN);
	while (i < size) {
		bytes_read = fread(buffer, sizeof(char), (size > MAXLEN) ? MAXLEN:size, input->f);
		i+= fwrite(buffer, sizeof(char), bytes_read, stdout);
	}
    free(buffer);
    jettison_inputstruct(input);
}

void process_dir(char *dirpath,JRB inodes){
    DIR *d;
    struct dirent *de;
    struct stat fileStat;
    char *fullpath = NULL;
    Dllist dirs,tmp;

    d = opendir(dirpath);
    if(d == NULL){
        perror(dirpath);
        exit(1);
    }

    dirs = new_dllist();
    for(de = readdir(d); de != NULL;  de = readdir(d)){
        /* Get Full Path */
	    fullpath = malloc(strlen(dirpath)+strlen(de->d_name)+2);
    	sprintf(fullpath,"%s/%s",dirpath,de->d_name);

        /* Check if it exists */
        if(lstat(fullpath,&fileStat) < 0){
            perror(fullpath);
            free(fullpath);
        }
        /* Check if it is a duplicate */
        else if(jrb_find_int(inodes,fileStat.st_ino) == NULL){
            jrb_insert_int(inodes,fileStat.st_ino,new_jval_s(fullpath));
            /* Write Dirs to the tar and prepare them to be processed */
            if(S_ISDIR(fileStat.st_mode) && strcmp(de->d_name,".") != 0 && strcmp(de->d_name,"..") != 0){
                write_info_to_tar(fullpath,fileStat);
                if(verbose) fprintf(stderr,"Directory: ./%s    %i bytes\n",fullpath,(int)fileStat.st_size);
                dll_append(dirs,new_jval_s(fullpath));
            }
            /* Write Files to Tar */
            else if(S_ISREG(fileStat.st_mode)){
                write_info_to_tar(fullpath,fileStat);
                if(verbose) fprintf(stderr,"File: ./%s    %i bytes\n",fullpath,(int)fileStat.st_size);
                write_contents_to_tar(fullpath,fileStat.st_size);
                free(fullpath);
            }
            else if(S_ISLNK(fileStat.st_mode)){
                if(verbose) fprintf(stderr,"Ignoring Soft Link: ./%s\n",fullpath);
            }
        }
        //Handle Duplicates
        else{
            if(strcmp(de->d_name,".") != 0 && strcmp(de->d_name,"..") != 0){
                write_info_to_tar(fullpath,fileStat);
                if(verbose) fprintf(stderr,"Link: ./%s\n",fullpath);
                free(fullpath);
            }
        }
    }
    closedir(d);

    //Process New Subdirs
    dll_traverse(tmp,dirs){
        process_dir(tmp->val.s,inodes);
        free(tmp->val.s);
    }
    free_dllist(dirs);
}

/*
 *
 * EXTRACT TAR FILES
 *
 * */
void extract_tar(){
    struct stat fileStat,*fileStatPtr;
    JRB inodes,dirs,tmp;
    char *name;

    /* Read from stdin */
    IS input = new_inputstruct(NULL);
    if(input == NULL){
        perror("jtar");
        exit(1);
    }
    inodes = make_jrb();
    dirs = make_jrb();

    /* While we can read */
    while(get_line(input) >= 0){
        /* Get name and stat */
        name = get_path(input->NF,input->fields);
        fread(&fileStat,sizeof(struct stat),1,stdin);

        /* Handle New Files */
        if(jrb_find_int(inodes,fileStat.st_ino) == NULL){
            jrb_insert_int(inodes,fileStat.st_ino,new_jval_s(name));
            /* Handle Dir */
            if(S_ISDIR(fileStat.st_mode) && strcmp(name,".") != 0&&strcmp(name,"..") != 0){
                if(verbose) fprintf(stderr,"Directory: ./%s    %i bytes\n",name,(int)fileStat.st_size);
                if(mkdir(name,fileStat.st_mode) < 0) perror(name);
                if(chmod(name,0777) < 0) perror(name);
                
                fileStatPtr = malloc(sizeof(struct stat));
                memcpy(fileStatPtr,&fileStat,sizeof(struct stat));
                jrb_insert_str(dirs,name,new_jval_v(fileStatPtr));
            }
            /* Handle Files */
            else if(S_ISREG(fileStat.st_mode)){
                if(verbose) fprintf(stderr,"File: ./%s    %i bytes\n",name,(int)fileStat.st_size);
                write_contents(name,fileStat.st_size);

                if(chmod(name,fileStat.st_mode) < 0) perror(name);
                struct utimbuf times;
                times.actime = fileStat.st_atime;
                times.modtime = fileStat.st_mtime;
                if(utime(name,&times) < 0) perror(name);
            }
        }
        /* Handle Links */
        else if(!S_ISDIR(fileStat.st_mode)){
            tmp = jrb_find_int(inodes,fileStat.st_ino);
            if(verbose) fprintf(stderr,"Link: ./%s to ./%s\n",name,tmp->val.s);
            if(link(tmp->val.s,name)) perror(name);
        }
    }
    /* Set Perms & Times for Dirs after the fact */
    jrb_traverse(tmp,dirs){
        fileStatPtr = tmp->val.v;
        if(chmod(tmp->key.s,fileStatPtr->st_mode) < 0) perror(name);
        struct utimbuf times;
        times.actime = fileStatPtr->st_atime;
        times.modtime = fileStatPtr->st_mtime;
        if(utime(tmp->key.s,&times) < 0) perror(name);
        free(tmp->key.s);
    }
    jrb_free_tree(inodes);
    jrb_free_tree(dirs);
}
char *get_path(int n, char **fields){
    /* Take char** from the Fields library and convert it to a string */
    int len=0,i=0;
    while(i<n) len+=strlen(fields[i++]);

    char *retval = calloc(sizeof(char),len+n);
    for(i=0;i<n;i++) strcat(retval,strdup(fields[i]));
    return retval;
}
void write_contents(char *filename,int size){
    int i;
    FILE *out;
    void *buf;

    out = fopen (filename, "w");
    if (out == NULL) fprintf (stderr, "Failed to extract %s\n",filename);

    buf = (void *) malloc (MAXLEN);
    while (size > 0) {
        i = fread (buf, 1, (size >= MAXLEN)?MAXLEN:size, stdin);
        size -= (out != NULL)?fwrite (buf, 1, i, out):i;
    }
    free (buf);
    if(out != NULL) fclose (out);
}
