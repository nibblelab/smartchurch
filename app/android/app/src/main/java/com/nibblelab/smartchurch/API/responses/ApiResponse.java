package com.nibblelab.smartchurch.API.responses;

public interface ApiResponse<T> {
    public void onResponse();
    public void onResponse(T data);
    public void onResponse(T data, int total);
    public void onAlreadyExecuted();
    public void onError(String msg);
    public void onFail(Object fail);
}
