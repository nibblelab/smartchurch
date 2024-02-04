package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class ResponseData<T> {

    @SerializedName("status")
    private String status;
    @SerializedName("success")
    private boolean success;
    @SerializedName("msg")
    private String msg;
    @SerializedName("data")
    private T data;
    @SerializedName("datas")
    private T datas;
    @SerializedName("token")
    private String token;
    @SerializedName("total")
    private int total;

    public ResponseData(String status, boolean success, String msg, T data, T datas, String token, int total) {
        this.status = status;
        this.success = success;
        this.msg = msg;
        this.data = data;
        this.datas = datas;
        this.token = token;
        this.total = total;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public boolean getSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public String getMsg() {
        return msg;
    }

    public void setMsg(String msg) {
        this.msg = msg;
    }

    public T getData() {
        return data;
    }

    public void setData(T data) {
        this.data = data;
    }

    public T getDatas() {
        return datas;
    }

    public void setDatas(T datas) {
        this.datas = datas;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }

    public boolean isSuccess() {
        return success;
    }

    public int getTotal() {
        return total;
    }

    public void setTotal(int total) {
        this.total = total;
    }

}
