package edu.usc.cs.weatherapps;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;

import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;
import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity {

	private RadioGroup radioUnitGroup;
	private RadioButton radioUnitButtonF;
	private RadioButton radioUnitButtonC;
	private String selectedType = "city";
	private EditText searchTxt;
	private String tempUnit = "F";
	private Button searchBtn;
	private int selectedId;
	private String url;

	// private TextView shareBtn;
	// private TextView shareForecastBtn;
	private JSONObject weather;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		searchTxt = (EditText) findViewById(R.id.edit_input);
		searchBtn = (Button) findViewById(R.id.input_button);
		radioUnitGroup = (RadioGroup) findViewById(R.id.radioUnit);
		radioUnitButtonF = (RadioButton) findViewById(R.id.radioF);
		radioUnitButtonC = (RadioButton) findViewById(R.id.radioC);

		searchBtn.setOnClickListener(new OnClickListener()

		{

			
			@Override
			public void onClick(View arg0) {
				// TODO Auto-generated method stub
				String query = searchTxt.getText().toString();
				if (query == null || query.equals("")) {
					Toast.makeText(arg0.getContext(),
							"Please enter the query string", Toast.LENGTH_SHORT)
							.show();
					return;
				}
				if (query.matches("\\d+(\\.\\d+)?")) {
					if (query.length() != 5) {
						Toast.makeText(
								arg0.getContext(),
								"Please enter a valid zip code, must be 5 digits",
								Toast.LENGTH_SHORT).show();
						return;
					}
					selectedType = "zip";
				} else if (query.contains(",")) {
					selectedType = "city";
				} else {
					Toast.makeText(
							arg0.getContext(),
							"Please enter a valid location, must include state and country separated by comma",
							Toast.LENGTH_SHORT).show();
					return;
				}

				selectedId = radioUnitGroup.getCheckedRadioButtonId();

				if (selectedId == radioUnitButtonF.getId()) {
					tempUnit = "f";
				}
				if (selectedId == radioUnitButtonC.getId()) {
					tempUnit = "c";
				}

				try {
					url = "http://cs-server.usc.edu:21642/Weather/searchservlet?"
							+ "data="
							+ URLEncoder.encode(query, "UTF-8")
							+ "&type=" + selectedType + "&unit=" + tempUnit;
				} catch (UnsupportedEncodingException e) {
					e.printStackTrace();
				}

				new FetchDataTask().execute(url);

				Toast.makeText(
						arg0.getContext(),
						"Input: " + query + " Type: " + selectedType
								+ " Unit : " + tempUnit + "  JSon" + url,
						Toast.LENGTH_SHORT).show();
			}

		});

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
