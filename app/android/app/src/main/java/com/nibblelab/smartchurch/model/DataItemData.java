package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class DataItemData {
    @SerializedName("value")
    private String value;
    @SerializedName("label")
    private String label;

    public DataItemData() {
    }

    public DataItemData(String value, String label) {
        this.value = value;
        this.label = label;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    public String getLabel() {
        return label;
    }

    public void setLabel(String label) {
        this.label = label;
    }
}
