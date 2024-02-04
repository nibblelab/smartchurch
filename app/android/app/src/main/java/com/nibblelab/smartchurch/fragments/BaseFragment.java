package com.nibblelab.smartchurch.fragments;

import android.os.Bundle;
import android.util.Log;

import androidx.fragment.app.Fragment;

import com.nibblelab.smartchurch.activity.Base;

public class BaseFragment extends Fragment {

    protected Base activity;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        activity = ((Base) getActivity());
    }

}
