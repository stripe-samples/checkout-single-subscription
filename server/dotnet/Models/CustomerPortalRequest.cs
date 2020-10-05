using Newtonsoft.Json;

public class CustomerPortalRequest
{
    [JsonProperty("customerId")]
    public string CustomerId { get; set; }
}
