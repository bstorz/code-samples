const int temperaturePin = 0;
const int ledPin = 10;

void setup(){
  Serial.begin(9600);
  pinMode(ledPin,OUTPUT);
}

void loop(){
    float voltage, degreesC, degreesF;
  
    voltage = (analogRead(temperaturePin) * 0.004882814);
    degreesC = (voltage - 0.5) * 100.0;  
    degreesF = (voltage - 0.5) * 100.0 * (9.0/5.0) + 32.0;

    Serial.println(voltage);
    delay(1000);
}
