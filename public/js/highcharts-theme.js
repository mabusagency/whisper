//Dark theme
Highcharts.theme = {
   colors: ["#af454f", "#f6c653", "#68baae","#203646","#87344c"],
   chart: {
      backgroundColor: "#ffffff",
      height:250,
      borderRadius:0,
      spacingBottom:0,
       type:'spline'
   },
   credits: {
      enabled: false
    },
    plotOptions: {
      series: {
          pointWidth: 13,
          marker: {
              fillColor: '#ffffff',
              symbol: 'circle',
              lineColor: '#282f4f',
              lineWidth: 1,
              radius: 0,
              states: {
                hover: {
                  fillColor: '#ffffff',
                  lineWidth: 2,
                  radius: 6,
                }
              }
          }
      },
      line: {
        states: {
          hover: {
            lineWidth: 0,
          }
        }
      },
      column: {
        borderWidth: 5,
        states: {
            hover: {
                color: '#d2e17a'
            }
        }
    }
   },
   title:{
      text:'',
   },
   legend:{
      enabled:false,
   },
   yAxis: {
      gridLineColor: '#ffffff',
      tickColor: '#ffffff',
      labels: {
          enabled: false
      },
      title: {
          text: null
      }
   },
   xAxis: {
      gridLineColor: '#f4f4f4',
      tickColor: '#f4f4f4',
      lineColor: '#f4f4f4',
      labels: {
          enabled: false
      },
      title: {
          text: null
      }
   },
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);