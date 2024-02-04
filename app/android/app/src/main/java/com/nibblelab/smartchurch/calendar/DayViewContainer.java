package com.nibblelab.smartchurch.calendar;

import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import com.kizitonwose.calendarview.model.CalendarDay;
import com.kizitonwose.calendarview.ui.ViewContainer;
import com.nibblelab.smartchurch.R;

public class DayViewContainer extends ViewContainer implements View.OnClickListener {

    public TextView day;
    public ImageView marker;
    public CalendarDay calendarDay;
    private OnDayViewContainerListener listener;

    public DayViewContainer(View view, OnDayViewContainerListener l) {
        super(view);

        day = (TextView) view.findViewById(R.id.calendarDayText);
        marker = (ImageView) view.findViewById(R.id.calendarDayMarker);

        marker.setVisibility(View.INVISIBLE);

        day.setOnClickListener(this);
        listener = l;
    }

    @Override
    public void onClick(View v) {
        listener.onDayViewContainerClick(calendarDay.getDate().getDayOfMonth(), calendarDay.getDate().getMonthValue(), calendarDay.getDate().getYear());
    }

    public interface OnDayViewContainerListener {
        void onDayViewContainerClick(int dia, int mes, int ano);
    }
}
