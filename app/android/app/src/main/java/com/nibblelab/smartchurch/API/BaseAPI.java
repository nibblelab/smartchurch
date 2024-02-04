package com.nibblelab.smartchurch.API;

import com.nibblelab.smartchurch.activity.Base;

import org.jetbrains.annotations.NotNull;

public class BaseAPI {

    protected String token;
    protected Base b;

    public BaseAPI(final Base b) {
        this.b = b;
    }

    public String getAuthToken() {
        return "token=" + b.getUserTokenValue();
    }

    public void checkNoInternetException(@NotNull String msg)
    {
        if(msg.contains("Unable to resolve host")) {
            this.b.showNoInternetWarning();
            this.b.stopInternetAutoTest();
            this.b.setInternetAutoTest();
        }
    }

}
