<div id="highcharts_<?php echo e($id); ?>">
  <highcharts :options="options" ref="highcharts"></highcharts>
  
</div>

<script>
        Vue.use(VueHighcharts);

        var options = {
          title: {
            text: '<?php echo e($title); ?>',
            x: -20 //center
          },
          subtitle: {
            text: '<?php echo e($description); ?>',
            x: -20
          },
          xAxis: {
            categories:  <?php echo $categories; ?>

          },
          yAxis: {
            title: {
              text: '<?php echo e($y_text); ?>'
            },
            plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
          },
          tooltip: {
            valueSuffix: '<?php echo e($value_suffix); ?>'
          },
          legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
          },
          series: <?php echo $series; ?>

        };
        
        var vm = new Vue({
          el: '#highcharts_<?php echo e($id); ?>',
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