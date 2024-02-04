package com.nibblelab.smartchurch.common;

public class StringHelper {

    public static boolean notEmpty(String s)
    {
        return (s != null && !s.equals(""));
    }

    public static boolean isDiff(String a, String b)
    {
        return (a != null && b != null && !a.equals(b));
    }
}
