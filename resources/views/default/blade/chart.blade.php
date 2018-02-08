<div id="highcharts_{{$id}}">
  <highcharts :options="options" ref="highcharts"></highcharts>
  {{--  <button @click="updateCredits">update credits</button>  --}}
</div>

<script>
        Vue.use(VueHighcharts);

        var options = {
          title: {
            text: '{{$title}}',
            x: -20 //center
          },
          subtitle: {
            text: '{{$description}}',
            x: -20
          },
          xAxis: {
            categories:  {!! $categories !!}
          },
          yAxis: {
            title: {
              text: '{{$y_text}}'
            },
            plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
          },
          tooltip: {
            valueSuffix: '{{$value_suffix}}'
          },
          legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
          },
          series: {!! $series !!}
        };
        
        var vm = new Vue({
          el: '#highcharts_{{$id}}',
          data: {
            options: options
          },
          methods: {
              updateCredits: function() {
                var chart = this.$refs.highcharts.chart;
              chart.credits.update({
                style: {
                  color: '#' + (Math.random() * 0xffffff | 0).toString(16)
                }
              });
            }
          }
        });
</script>