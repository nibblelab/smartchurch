package com.nibblelab.smartchurch.storage;

import android.content.Context;

import com.nibblelab.smartchurch.model.UserData;

public class UserStorage extends Storage<UserData> {

    public UserStorage(Context c)
    {
        super(UserData.class, c);
    }

    public UserStorage(Context c, String filename)
    {
        super(UserData.class, c, filename);
    }
}
