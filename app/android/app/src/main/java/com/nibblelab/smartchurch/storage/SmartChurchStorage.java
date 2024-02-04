package com.nibblelab.smartchurch.storage;

import android.content.Context;

import com.nibblelab.smartchurch.model.SmartChurchData;

public class SmartChurchStorage extends Storage<SmartChurchData> {

    public SmartChurchStorage(Context c)
    {
        super(SmartChurchData.class, c);
    }

    public SmartChurchStorage(Context c, String filename)
    {
        super(SmartChurchData.class, c, filename);
    }
}
