package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.Date;

public class SmartChurchData {
    @SerializedName("last_acs")
    private Date lastAcs;

    public SmartChurchData() {
    }

    public SmartChurchData(Date lastAcs) {
        this.lastAcs = lastAcs;
    }

    public Date getLastAcs() {
        return lastAcs;
    }

    public void setLastAcs(Date lastAcs) {
        this.lastAcs = lastAcs;
    }
}
