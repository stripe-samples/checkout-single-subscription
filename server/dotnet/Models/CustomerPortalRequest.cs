using Newtonsoft.Json;

public class CustomerPortalRequest
{
    [JsonProperty("sessionId")]
    public string SessionId { get; set; }
}
