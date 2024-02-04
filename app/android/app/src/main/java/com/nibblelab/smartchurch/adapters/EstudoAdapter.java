package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.EstudoData;
import com.nibblelab.smartchurch.ui.events.EstudoListEvents;
import com.squareup.picasso.Picasso;

import java.util.List;

public class EstudoAdapter extends RecyclerView.Adapter<EstudoAdapter.RecyclerEstudoViewHolder> {

    public static EstudoListEvents events;
    Context ctx;
    private List<EstudoData> list;

    public EstudoAdapter(Context ctx, List<EstudoData> list, EstudoListEvents ev)
    {
        this.ctx = ctx;
        this.list = list;
        this.events = ev;
    }

    @NonNull
    @Override
    public EstudoAdapter.RecyclerEstudoViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i)
    {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.estudo_it_layout, viewGroup, false);
        return new EstudoAdapter.RecyclerEstudoViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull EstudoAdapter.RecyclerEstudoViewHolder viewHolder, int i) {
        EstudoData c = list.get(i);
        viewHolder.estudoTitulo.setText(c.getTitulo());

        // imagem do sermão
        if(StringHelper.notEmpty(c.getLogoApp()))
        {
            Picasso.get().load(ApiContants.RC_URL + "/" + c.getLogoApp()).into(viewHolder.estudoImg);
        }
        else if(StringHelper.notEmpty(c.getLogo()))
        {
            Picasso.get().load(ApiContants.RC_URL + "/" + c.getLogo()).into(viewHolder.estudoImg);
        }

    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    protected class RecyclerEstudoViewHolder extends RecyclerView.ViewHolder
    {
        protected LinearLayout estudoWrapper;
        protected TextView estudoTitulo;
        protected ImageView estudoImg;

        public RecyclerEstudoViewHolder(final View v)
        {
            super(v);

            estudoWrapper = (LinearLayout) v.findViewById(R.id.estudo_it_layout);
            estudoTitulo = (TextView) v.findViewById(R.id.estudo_titulo);
            estudoImg = (ImageView) v.findViewById(R.id.estudo_img);

            estudoWrapper.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    events.onSelectEstudo(list.get(getLayoutPosition()));
                }
            });
        }
    }
}
