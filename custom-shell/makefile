# CS360 Class Manager
# August 29, 2005
CC = gcc

CLASSDIR = /home/huangj/cs360
INCLUDES = $(CLASSDIR)/include
CFLAGS = -g -I$(INCLUDES)

CDIR = /home/cs360
LIBDIR = $(CDIR)/pub
LIBS = $(LIBDIR)/libfdr.a

EXECUTABLES = jsh3

all: $(EXECUTABLES)

clean:
	rm -f core $(EXECUTABLES) *.o

.SUFFIXES: .c .o
.c.o:
	$(CC) $(CFLAGS) -c $*.c

jsh3: jsh3.o
	$(CC) -g -o jsh3 jsh3.o $(LIBS)
