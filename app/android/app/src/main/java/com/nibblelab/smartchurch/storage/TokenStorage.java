package com.nibblelab.smartchurch.storage;

import android.content.Context;

import com.nibblelab.smartchurch.model.TokenData;

public class TokenStorage extends Storage<TokenData> {

    public TokenStorage(Context c)
    {
        super(TokenData.class, c);
    }

    public TokenStorage(Context c, String filename)
    {
        super(TokenData.class, c, filename);
    }
}
