#!/usr/bin/python
# -*- coding: utf-8 -*-

import serial
import time
import mysql.connector
import sys

cnx = mysql.connector.connect(user='root',password='root',host='127.0.0.1',database='temp_mon')
cursor = cnx.cursor()
emp_no = cursor.lastrowid

ser = serial.Serial(sys.argv[1], sys.argv[2])
from datetime import datetime
while 1:
	myFile = open('output.txt','a')
	st = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	st = st.strip()
	temp = str(ser.readline(),encoding='utf8');
	temp = temp.strip();
	if(temp.count(".") > 1):
		continue
	if(float(temp) > 2 or float(temp) < .5):
		 continue

	add_temp = ("INSERT INTO temp (timestamp,temp) VALUES (%s,%s)")
	temp_data = (st,temp)
	cursor.execute(add_temp,temp_data)
	myFile.write(st+ " " + str(ser.readline(),encoding='utf8'))
	sys.stdout.write(st+ " " +str(ser.readline(),encoding='utf8'))
	myFile.close()
	cnx.commit()
cursor.close()
cnx.close()
