package com.nibblelab.smartchurch.calendar;

import android.view.View;
import android.widget.TableLayout;
import android.widget.TextView;

import com.kizitonwose.calendarview.ui.ViewContainer;
import com.nibblelab.smartchurch.R;

public class MonthHeaderViewContainer extends ViewContainer {

    public TextView month;
    public TableLayout weekdays;

    public MonthHeaderViewContainer(View view) {
        super(view);

        month = (TextView) view.findViewById(R.id.monthHeaderText);
        weekdays = (TableLayout) view.findViewById(R.id.week_days);
    }
}
