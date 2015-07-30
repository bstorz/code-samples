CC = gcc
CLASSDIR = /home/huangj/cs360
CDIR = /home/cs360
INCLUDES = $(CLASSDIR)/include
CFLAGS = -g -I$(INCLUDES)
LIBDIR = $(CDIR)/pub
LIBS = $(LIBDIR)/libfdr.a
EXECUTABLES = jtar

all: $(EXECUTABLES)

clean:
	rm -f core $(EXECUTABLES) *.o

.SUFFIXES: .c .o
.c.o:
	$(CC) $(CFLAGS) -c $*.c

jtar: jtar.o
	$(CC) -g -o jtar jtar.o $(LIBS)
