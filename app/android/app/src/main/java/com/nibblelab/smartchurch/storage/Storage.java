package com.nibblelab.smartchurch.storage;

import android.content.Context;

import com.google.gson.Gson;

import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;

public abstract class Storage<T> {
    private String filename;
    private Gson gson;
    private Context c;
    protected Class<T> typeOfT;

    public Storage(Class<T> t, Context c) {
        this.gson = new Gson();
        this.typeOfT = t;
        this.c = c;
    }

    public Storage(Class<T> t, Context c, String filename) {
        this.gson = new Gson();
        this.typeOfT = t;
        this.c = c;
        this.filename = filename;
    }

    public String getFilename() {
        return filename;
    }

    public void setFilename(String filename) {
        this.filename = filename;
    }

    public Context getC() {
        return c;
    }

    public void setC(Context c) {
        this.c = c;
    }

    private void writeToFile(String content) throws IOException
    {
        File file = new File(this.c.getFilesDir(), this.filename);
        FileWriter fw = new FileWriter(file, false);
        fw.write(content);
        fw.flush();
        fw.close();
    }

    public void write(T data) throws IOException {

        this.writeToFile(this.gson.toJson(data));
    }

    public void write(T data, Context c, String filename) throws IOException {
        this.c = c;
        this.filename = filename;
        this.write(data);
    }

    private String readFromFile() throws IOException {
        File file = new File(this.c.getFilesDir(), this.filename);
        FileReader fr = new FileReader(file);
        int i;
        StringBuilder sb = new StringBuilder("");
        String result = null;

        while((i=fr.read())!=-1)
        {
            sb.append((char)i);
        }

        result = sb.toString();

        fr.close();

        return result;
    }

    public T read() throws IOException {

        String content = this.readFromFile();
        if(content == null || content.equals(""))
        {
            return null;
        }
        return this.gson.fromJson(content, this.typeOfT);
    }

    public T read(Context c, String filename) throws IOException {
        this.c = c;
        this.filename = filename;
        return this.read();
    }
}
