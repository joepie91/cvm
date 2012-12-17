<?php
/*
function test_iprange($input, $desired_start, $desired_end, $desired_size, $desired_type)
{
	try
	{
		$obj = new IpRange($input);
		
		if($obj->sStart != $desired_start)
		{
			echo("IpRange unit test failed due to sStart mismatch. Expected: {$desired_start} &nbsp;&nbsp; Actual: {$obj->sStart}<br>");
		}
		
		if($obj->sEnd != $desired_end)
		{
			echo("IpRange unit test failed due to sEnd mismatch. Expected: {$desired_end} &nbsp;&nbsp; Actual: {$obj->sEnd}<br>");
		}
		
		if($obj->sSize != $desired_size)
		{
			echo("IpRange unit test failed due to sSize mismatch. Expected: {$desired_size} &nbsp;&nbsp; Actual: {$obj->sSize}<br>");
		}
		
		if($obj->sType != $desired_type)
		{
			echo("IpRange unit test failed due to sType mismatch. Expected: {$desired_type} &nbsp;&nbsp; Actual: {$obj->sType}<br>");
		}
		
	}
	catch (Exception $e)
	{
		echo("IpRange unit test failed due to exception! Input: {$input} &nbsp;&nbsp; Error message: " . $e->getMessage() . "<br>");
	}
}

test_iprange("fe80:0000:0000:0000:e0d3:f0ff:fe28:5f47/64", "fe80:0:0:0:0:0:0:0", "fe80:0:0:0:ffff:ffff:ffff:ffff", 64, 6);
test_iprange("fe80:0000:0000:0000:e0d3:f0ff:fe28:5f47", "fe80:0000:0000:0000:e0d3:f0ff:fe28:5f47", "fe80:0000:0000:0000:e0d3:f0ff:fe28:5f47", 0, 6);
test_iprange("0.0.0.0/1", "0.0.0.0", "127.255.255.255", 1, 4);
test_iprange("162.16.47.0/16", "162.16.0.0", "162.16.255.255", 16, 4);
test_iprange("192.168.1.0/27", "192.168.1.0", "192.168.1.31", 27, 4);
test_iprange("192.168.1.0/32", "192.168.1.0", "192.168.1.0", 32, 4);
test_iprange("192.168.1.0", "192.168.1.0", "192.168.1.0", 0, 4);

$sVps = new Vps(0);
$sVps->uHostname = "test6.cryto.net";
$sVps->uInternalId = "110";
$sVps->uNodeId = 2;
$sVps->uTemplateId = 1;
$sVps->uUserId = 1;
$sVps->uVirtualizationType = CVM_VIRTUALIZATION_OPENVZ;
$sVps->uGuaranteedRam = 256;
$sVps->uBurstableRam = 384;
$sVps->uDiskSpace = 6000;
$sVps->uCpuCount = 1;
$sVps->uStatus = CVM_STATUS_BLANK;
$sVps->uIncomingTrafficLimit = 500000000000;
$sVps->uOutgoingTrafficLimit = 500000000000;
$sVps->uTotalTrafficLimit = 1000000000000;
$sVps->InsertIntoDatabase();

$sVps->Deploy();
*/
/*
var_dump(
	parse_size("15m"),	parse_size("24 KB"),	parse_size("51"),
	parse_size("2 TiB"),	parse_size("4.9GiB"),	parse_size("0.75GB"),
	parse_size("20gb", 1000),	parse_size("14.6 TiB", 1000),	parse_size("84YB")
);
*/
/*
var_dump(first_unused_ctid());
*/

var_dump(format_size(900), format_size(900000), format_size(900000000), format_size(900000000000), format_size(900000000000000), format_size(9000000000000000));