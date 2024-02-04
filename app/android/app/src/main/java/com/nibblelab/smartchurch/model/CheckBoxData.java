package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class CheckBoxData {
    @SerializedName("checked")
    private boolean checked;
    @SerializedName("id")
    private String id;

    public CheckBoxData() {
    }

    public CheckBoxData(boolean checked, String id) {
        this.checked = checked;
        this.id = id;
    }

    public boolean isChecked() {
        return checked;
    }

    public void setChecked(boolean checked) {
        this.checked = checked;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
}
