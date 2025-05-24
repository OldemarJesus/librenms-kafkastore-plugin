@extends('layouts.librenmsv1')

@section('title', 'Kafka Datastore Plugin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-server"></i> Kafka Datastore Plugin for LibreNMS
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Overview</h4>
                            <p>
                                The Kafka Datastore Plugin allows LibreNMS to send monitoring metrics to Apache Kafka 
                                for real-time streaming and processing. This enables integration with big data platforms, 
                                stream processing frameworks, and other monitoring tools.
                            </p>
                            
                            <h4>Features</h4>
                            <ul>
                                <li><strong>Real-time Metrics Streaming:</strong> Send LibreNMS metrics to Kafka topics in real-time</li>
                                <li><strong>SSL/TLS Support:</strong> Secure connections with SSL/TLS encryption</li>
                                <li><strong>SASL Authentication:</strong> Support for PLAIN, SCRAM-SHA-256, SCRAM-SHA-512, and GSSAPI</li>
                                <li><strong>Multiple Message Formats:</strong> JSON, InfluxDB Line Protocol, and Prometheus formats</li>
                                <li><strong>Batch Processing:</strong> Configurable batch sizes for optimal performance</li>
                                <li><strong>Connection Testing:</strong> Built-in connection testing functionality</li>
                            </ul>

                            <h4>Configuration</h4>
                            <p>
                                Configure your Kafka settings in the 
                                <a href="{{ route('kafkastore-plugin.settings') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-cogs"></i> Settings
                                </a> 
                                page to start sending metrics to your Kafka cluster.
                            </p>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-info-circle"></i> Status
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    @if(isset($status))
                                        <div class="row">
                                            <div class="col-sm-6"><strong>Status:</strong></div>
                                            <div class="col-sm-6">
                                                <span class="label label-{{ $status['enabled'] ? 'success' : 'default' }}">
                                                    {{ $status['enabled'] ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </div>
                                        </div>
                                        <br>
                                        
                                        @if($status['enabled'])
                                            <div class="row">
                                                <div class="col-sm-6"><strong>Brokers:</strong></div>
                                                <div class="col-sm-6">{{ $status['brokers'] ?? 'Not configured' }}</div>
                                            </div>
                                            <br>
                                            
                                            <div class="row">
                                                <div class="col-sm-6"><strong>Topic:</strong></div>
                                                <div class="col-sm-6">{{ $status['topic'] ?? 'Not configured' }}</div>
                                            </div>
                                            <br>
                                            
                                            <div class="row">
                                                <div class="col-sm-6"><strong>SSL:</strong></div>
                                                <div class="col-sm-6">
                                                    <span class="label label-{{ $status['ssl_enabled'] ? 'success' : 'default' }}">
                                                        {{ $status['ssl_enabled'] ? 'Enabled' : 'Disabled' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                            
                                            <div class="row">
                                                <div class="col-sm-6"><strong>Format:</strong></div>
                                                <div class="col-sm-6">{{ $status['format'] ?? 'JSON' }}</div>
                                            </div>
                                            <br>
                                            
                                            <div class="row">
                                                <div class="col-sm-6"><strong>Last Send:</strong></div>
                                                <div class="col-sm-6">{{ $status['last_send'] ?? 'Never' }}</div>
                                            </div>
                                        @endif
                                    @else
                                        <p>Plugin status information is not available.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-exclamation-triangle"></i> Requirements
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-check text-success"></i> PHP Kafka Extension</li>
                                        <li><i class="fa fa-check text-success"></i> Apache Kafka Cluster</li>
                                        <li><i class="fa fa-info-circle text-info"></i> Network connectivity to Kafka brokers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Panel -->
            @if(isset($recent_activity) && count($recent_activity) > 0)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-clock-o"></i> Recent Activity
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Event</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_activity as $activity)
                                <tr>
                                    <td>{{ $activity['timestamp'] }}</td>
                                    <td>{{ $activity['event'] }}</td>
                                    <td>{{ $activity['details'] }}</td>
                                    <td>
                                        <span class="label label-{{ $activity['status'] == 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($activity['status']) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
