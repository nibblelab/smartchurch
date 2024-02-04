package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.SermaoData;
import com.nibblelab.smartchurch.ui.events.SermaoListEvents;
import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.squareup.picasso.Picasso;

import java.util.List;

public class SermaoAdapter extends RecyclerView.Adapter<SermaoAdapter.RecyclerSermaoViewHolder> {

    public static SermaoListEvents events;
    Context ctx;
    private List<SermaoData> list;

    public SermaoAdapter(Context ctx, List<SermaoData> list, SermaoListEvents ev)
    {
        this.ctx = ctx;
        this.list = list;
        this.events = ev;
    }

    @NonNull
    @Override
    public RecyclerSermaoViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i)
    {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.sermao_it_layout, viewGroup, false);
        return new RecyclerSermaoViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerSermaoViewHolder viewHolder, int i) {
        SermaoData c = list.get(i);
        viewHolder.sermaoTitulo.setText(c.getTitulo());

        // imagem do sermão
        if(StringHelper.notEmpty(c.getLogoApp()))
        {
            Picasso.get().load(ApiContants.RC_URL + "/" + c.getLogoApp()).into(viewHolder.sermaoImg);
        }
        else if(StringHelper.notEmpty(c.getLogo()))
        {
            Picasso.get().load(ApiContants.RC_URL + "/" + c.getLogo()).into(viewHolder.sermaoImg);
        }

    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    protected class RecyclerSermaoViewHolder extends RecyclerView.ViewHolder
    {
        protected LinearLayout sermaoWrapper;
        protected TextView sermaoTitulo;
        protected ImageView sermaoImg;

        public RecyclerSermaoViewHolder(final View v)
        {
            super(v);

            sermaoWrapper = (LinearLayout) v.findViewById(R.id.sermao_it_layout);
            sermaoTitulo = (TextView) v.findViewById(R.id.sermao_titulo);
            sermaoImg = (ImageView) v.findViewById(R.id.sermao_img);

            sermaoWrapper.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    events.onSelectSermao(list.get(getLayoutPosition()));
                }
            });
        }
    }
}
