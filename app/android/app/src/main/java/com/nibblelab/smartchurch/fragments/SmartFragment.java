package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;

import com.budiyev.android.codescanner.CodeScanner;
import com.budiyev.android.codescanner.CodeScannerView;
import com.budiyev.android.codescanner.DecodeCallback;
import com.google.zxing.Result;
import com.nibblelab.smartchurch.R;

public class SmartFragment extends BaseFragment {

    private SmartFragment.OnSmartFragInteractionListener mListener;
    private CodeScanner mCodeScanner;

    private static final String TAG = "SmartFragment";

    public SmartFragment() {

    }

    public static SmartFragment newInstance() {
        SmartFragment fragment = new SmartFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {

        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View inf = inflater.inflate(R.layout.fragment_smart, container, false);

        activity.setToolbarTitle(R.string.menu_smart);

        CodeScannerView scannerView = inf.findViewById(R.id.scanner_view);
        mCodeScanner = new CodeScanner(getContext(), scannerView);
        mCodeScanner.setDecodeCallback(new DecodeCallback() {
            @Override
            public void onDecoded(@NonNull final Result result) {
                activity.runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Toast.makeText(activity, result.getText(), Toast.LENGTH_SHORT).show();
                    }
                });
            }
        });
        scannerView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                mCodeScanner.startPreview();
            }
        });

        return inf;
    }

    @Override
    public void onResume() {
        super.onResume();
        if(mCodeScanner != null) mCodeScanner.startPreview();
    }

    @Override
    public void onPause() {
        if(mCodeScanner != null) mCodeScanner.releaseResources();
        super.onPause();
    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onSmartFragInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof SmartFragment.OnSmartFragInteractionListener) {
            mListener = (SmartFragment.OnSmartFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnSmartFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnSmartFragInteractionListener {
        void onSmartFragInteraction();
    }
}
