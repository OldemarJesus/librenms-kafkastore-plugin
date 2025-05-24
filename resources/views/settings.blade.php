@extends('layouts.librenmsv1')

@section('title', 'Kafka Datastore Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-cogs"></i> Kafka Datastore Configuration
                    </h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="" class="form-horizontal">
                        @csrf
                        
                        <!-- Enable/Disable Kafka -->
                        <div class="form-group">
                            <label for="kafka_enable" class="col-sm-3 control-label">Enable Kafka Datastore</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_enable" 
                                       name="kafka_enable" 
                                       value="1" 
                                       {{ ($settings['kafka_enable'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable sending metrics to Kafka</span>
                            </div>
                        </div>

                        <!-- Kafka Brokers -->
                        <div class="form-group">
                            <label for="kafka_brokers" class="col-sm-3 control-label">Kafka Brokers</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_brokers" 
                                       name="kafka_brokers" 
                                       value="{{ $settings['kafka_brokers'] ?? 'localhost:9092' }}"
                                       placeholder="broker1:9092,broker2:9092">
                                <span class="help-block">Comma-separated list of Kafka broker addresses</span>
                            </div>
                        </div>

                        <!-- Kafka Topic -->
                        <div class="form-group">
                            <label for="kafka_topic" class="col-sm-3 control-label">Kafka Topic</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_topic" 
                                       name="kafka_topic" 
                                       value="{{ $settings['kafka_topic'] ?? 'librenms-metrics' }}"
                                       placeholder="librenms-metrics">
                                <span class="help-block">Topic name for LibreNMS metrics</span>
                            </div>
                        </div>

                        <!-- SSL Configuration -->
                        <div class="form-group">
                            <label for="kafka_ssl_enable" class="col-sm-3 control-label">Enable SSL/TLS</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_ssl_enable" 
                                       name="kafka_ssl_enable" 
                                       value="1" 
                                       {{ ($settings['kafka_ssl_enable'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable SSL/TLS encryption for Kafka connection</span>
                            </div>
                        </div>

                        <!-- SSL Certificate Path -->
                        <div class="form-group ssl-config" style="{{ ($settings['kafka_ssl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_ssl_ca_cert" class="col-sm-3 control-label">CA Certificate Path</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_ssl_ca_cert" 
                                       name="kafka_ssl_ca_cert" 
                                       value="{{ $settings['kafka_ssl_ca_cert'] ?? '' }}"
                                       placeholder="/path/to/ca-cert.pem">
                                <span class="help-block">Path to CA certificate file</span>
                            </div>
                        </div>

                        <!-- SSL Client Certificate -->
                        <div class="form-group ssl-config" style="{{ ($settings['kafka_ssl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_ssl_cert" class="col-sm-3 control-label">Client Certificate Path</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_ssl_cert" 
                                       name="kafka_ssl_cert" 
                                       value="{{ $settings['kafka_ssl_cert'] ?? '' }}"
                                       placeholder="/path/to/client-cert.pem">
                                <span class="help-block">Path to client certificate file (optional)</span>
                            </div>
                        </div>

                        <!-- SSL Client Key -->
                        <div class="form-group ssl-config" style="{{ ($settings['kafka_ssl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_ssl_key" class="col-sm-3 control-label">Client Key Path</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_ssl_key" 
                                       name="kafka_ssl_key" 
                                       value="{{ $settings['kafka_ssl_key'] ?? '' }}"
                                       placeholder="/path/to/client-key.pem">
                                <span class="help-block">Path to client private key file (optional)</span>
                            </div>
                        </div>

                        <!-- SASL Authentication -->
                        <div class="form-group">
                            <label for="kafka_sasl_enable" class="col-sm-3 control-label">Enable SASL Authentication</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_sasl_enable" 
                                       name="kafka_sasl_enable" 
                                       value="1" 
                                       {{ ($settings['kafka_sasl_enable'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable SASL authentication for Kafka</span>
                            </div>
                        </div>

                        <!-- SASL Mechanism -->
                        <div class="form-group sasl-config" style="{{ ($settings['kafka_sasl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_sasl_mechanism" class="col-sm-3 control-label">SASL Mechanism</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="kafka_sasl_mechanism" name="kafka_sasl_mechanism">
                                    <option value="PLAIN" {{ ($settings['kafka_sasl_mechanism'] ?? 'PLAIN') == 'PLAIN' ? 'selected' : '' }}>PLAIN</option>
                                    <option value="SCRAM-SHA-256" {{ ($settings['kafka_sasl_mechanism'] ?? '') == 'SCRAM-SHA-256' ? 'selected' : '' }}>SCRAM-SHA-256</option>
                                    <option value="SCRAM-SHA-512" {{ ($settings['kafka_sasl_mechanism'] ?? '') == 'SCRAM-SHA-512' ? 'selected' : '' }}>SCRAM-SHA-512</option>
                                    <option value="GSSAPI" {{ ($settings['kafka_sasl_mechanism'] ?? '') == 'GSSAPI' ? 'selected' : '' }}>GSSAPI</option>
                                </select>
                                <span class="help-block">SASL authentication mechanism</span>
                            </div>
                        </div>

                        <!-- SASL Username -->
                        <div class="form-group sasl-config" style="{{ ($settings['kafka_sasl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_sasl_username" class="col-sm-3 control-label">SASL Username</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_sasl_username" 
                                       name="kafka_sasl_username" 
                                       value="{{ $settings['kafka_sasl_username'] ?? '' }}"
                                       placeholder="kafka-user">
                                <span class="help-block">SASL username</span>
                            </div>
                        </div>

                        <!-- SASL Password -->
                        <div class="form-group sasl-config" style="{{ ($settings['kafka_sasl_enable'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_sasl_password" class="col-sm-3 control-label">SASL Password</label>
                            <div class="col-sm-9">
                                <input type="password" 
                                       class="form-control" 
                                       id="kafka_sasl_password" 
                                       name="kafka_sasl_password" 
                                       value="{{ $settings['kafka_sasl_password'] ?? '' }}"
                                       placeholder="password">
                                <span class="help-block">SASL password</span>
                            </div>
                        </div>

                        <!-- Message Format -->
                        <div class="form-group">
                            <label for="kafka_message_format" class="col-sm-3 control-label">Message Format</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="kafka_message_format" name="kafka_message_format">
                                    <option value="json" {{ ($settings['kafka_message_format'] ?? 'json') == 'json' ? 'selected' : '' }}>JSON</option>
                                    <option value="influxdb" {{ ($settings['kafka_message_format'] ?? '') == 'influxdb' ? 'selected' : '' }}>InfluxDB Line Protocol</option>
                                    <option value="prometheus" {{ ($settings['kafka_message_format'] ?? '') == 'prometheus' ? 'selected' : '' }}>Prometheus Format</option>
                                </select>
                                <span class="help-block">Format for metrics messages sent to Kafka</span>
                            </div>
                        </div>

                        <!-- Batch Size -->
                        <div class="form-group">
                            <label for="kafka_batch_size" class="col-sm-3 control-label">Batch Size</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_batch_size" 
                                       name="kafka_batch_size" 
                                       value="{{ $settings['kafka_batch_size'] ?? 100 }}"
                                       min="1" 
                                       max="10000">
                                <span class="help-block">Number of metrics to batch before sending to Kafka</span>
                            </div>
                        </div>

                        <!-- Test Connection Button -->
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="button" class="btn btn-info" id="test-connection">
                                    <i class="fa fa-plug"></i> Test Connection
                                </button>
                                <div id="test-result" class="alert" style="display: none; margin-top: 10px;"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle SSL configuration fields
    $('#kafka_ssl_enable').change(function() {
        if ($(this).is(':checked')) {
            $('.ssl-config').show();
        } else {
            $('.ssl-config').hide();
        }
    });

    // Toggle SASL configuration fields
    $('#kafka_sasl_enable').change(function() {
        if ($(this).is(':checked')) {
            $('.sasl-config').show();
        } else {
            $('.sasl-config').hide();
        }
    });

    // Test connection functionality
    $('#test-connection').click(function() {
        var button = $(this);
        var resultDiv = $('#test-result');
        
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
        
        $.ajax({
            url: '',
            method: 'POST',
            data: $('form').serialize(),
            success: function(response) {
                if (response.success) {
                    resultDiv.removeClass('alert-danger').addClass('alert-success')
                           .html('<i class="fa fa-check"></i> Connection successful!')
                           .show();
                } else {
                    resultDiv.removeClass('alert-success').addClass('alert-danger')
                           .html('<i class="fa fa-times"></i> Connection failed: ' + response.message)
                           .show();
                }
            },
            error: function() {
                resultDiv.removeClass('alert-success').addClass('alert-danger')
                       .html('<i class="fa fa-times"></i> Connection test failed')
                       .show();
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Connection');
            }
        });
    });
});
</script>
@endsection
