package com.nibblelab.smartchurch.API;

import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.API.responses.GenericError;
import com.nibblelab.smartchurch.API.responses.GenericResponse;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;

/**
 * Objeto genérico para realizar a chamada de busca na API e evitar que o código dos fragments
 * fique muito grande, com chamadas parecidas repetindo toda hora
 *
 * @param <T>
 */
public class GenericAPI<T> {

    public void getAll(BaseAPI obj, String methodName, GenericResponse<T> response, GenericError error)
    {
        try {
            Method method = obj.getClass().getMethod(methodName, ApiResponse.class);
            method.invoke(obj, new ApiResponse<T>() {
                @Override
                public void onResponse() {

                }

                @Override
                public void onResponse(T data) {

                }

                @Override
                public void onResponse(T data, int total) {
                    response.getResponse(data);
                }

                @Override
                public void onAlreadyExecuted() {

                }

                @Override
                public void onError(String msg) {
                    error.getError(msg);
                }

                @Override
                public void onFail(Object fail) {
                    error.getError(fail.toString());
                }
            });
        } catch (SecurityException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (IllegalArgumentException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        }
    }

    public void getAllBy(BaseAPI obj, String methodName, String param, GenericResponse<T> response, GenericError error)
    {
        try {
            Method method = obj.getClass().getMethod(methodName, String.class, ApiResponse.class);
            method.invoke(obj, param, new ApiResponse<T>() {
                @Override
                public void onResponse() {

                }

                @Override
                public void onResponse(T data) {

                }

                @Override
                public void onResponse(T data, int total) {
                    response.getResponse(data);
                }

                @Override
                public void onAlreadyExecuted() {

                }

                @Override
                public void onError(String msg) {
                    error.getError(msg);
                }

                @Override
                public void onFail(Object fail) {
                    error.getError(fail.toString());
                }
            });
        } catch (SecurityException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (IllegalArgumentException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        }
    }
}
